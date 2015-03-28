#include "NotificationProcessor.h"
#include "../Controller/PartnerInfoController.h"
#include "../Controller/NotificationController.h"
#include "../Model/NotificationModel.h"
#include <pthread.h>

CNotificationProcessor::CNotificationProcessor(const string& strFileName)
:CProcessor(strFileName)
{
	m_pPartnerInfoController = new CPartnerInfoController();
}

CNotificationProcessor::~CNotificationProcessor(void)
{
	if (NULL != m_pPartnerInfoController)
	{
		delete m_pPartnerInfoController;
	}

	if (NULL != m_pNotificationController)
	{
		delete m_pNotificationController;
	}

	m_arrNotificationModel.clear();
}

bool CNotificationProcessor::Connect()
{		
	// Register controllers before connecting to database
	RegisterController(m_pPartnerInfoController);
	RegisterController(m_pNotificationController);

	return CProcessor::Connect();
}

auto_ptr<DBClientCursor> CNotificationProcessor::LoadNotificationInfo()
{
	auto_ptr<DBClientCursor> ptrResultCursor = auto_ptr<DBClientCursor>();
	try
	{
		ptrResultCursor = m_pNotificationController->Find(QUERY("noti_status"<<0));
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CNotificationProcessor", "LoadNotificationInfo()","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}
	return ptrResultCursor;
}

void CNotificationProcessor::ProceedNotifyInfo()
{
	if (!Connect())
	{
		return;
	}

	if (!m_pPartnerInfoController->LoadPartnerInfo())
	{
		return;
	}
	
	while (true)
	{		
		auto_ptr<DBClientCursor> ptrInfoNotifyCursor = LoadNotificationInfo();
		if (ptrInfoNotifyCursor->more())
		{			
			while (ptrInfoNotifyCursor->more()) {
				BSONObj oInfoNotify = ptrInfoNotifyCursor->nextSafe();
				GetPartnersFromRecord(oInfoNotify);	
				DispatchToPartners(oInfoNotify);
			}

			sleep(LOAD_INFO_DELAY);
		}
		else
		{
			//Sleep for a while
			sleep(LOAD_INFO_CHANGE_CYCLE);
		}

		ptrInfoNotifyCursor.reset();
		usleep(NOTI_PROCESS_DELAY);
	}
}

void CNotificationProcessor::GetPartnersFromRecord(const BSONObj& boRecord)
{
	if (m_arrNotificationModel.size() > 0)
	{
		return;
	}

	string strTableName = m_pNotificationController->GetTableName();
	StringSet setFieldNames;
	// Get all field names of the record
	boRecord.getFieldNames(setFieldNames);

	StringSet::iterator it = setFieldNames.begin();
	while (it != setFieldNames.end())
	{
		// Get notification model from field name
		CNotificationModel* pNotificationModel = m_pPartnerInfoController->GetNotificationModel(*it, strTableName);
		if (NULL != pNotificationModel)
		{
			m_arrNotificationModel.push_back(pNotificationModel);
		}
		it++;
	}
}

void CNotificationProcessor::DispatchToPartners(const BSONObj& boRecord)
{	
	pthread_t arrThread[m_arrNotificationModel.size()];
	for(int i=0; i < m_arrNotificationModel.size(); i++) {
		try{			
			CNotificationModel* pNotificationModel = m_arrNotificationModel[i];
			// Check if data is not dispatched to partner
			if (!boRecord.getBoolField(pNotificationModel->GetPartnerName().c_str()))
			{
				pNotificationModel->SetData(boRecord);
				pNotificationModel->SetNotification(false);
				// Create thread to dispatch data
				int iResult = pthread_create(&arrThread[i], NULL, m_pFuncStartDispatcher, pNotificationModel);
				if (iResult) {
					CUtilities::WriteErrorLog(ERROR_MSG, CUtilities::FormatLog(ERROR_MSG, "CNotificationProcessor", "DispatchToPartners()", "FAIL CREATE THREAD"));
					continue;
				}
			}
			else
			{
				// Notified to partner
				pNotificationModel->SetNotification();				
			}
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			string strLog;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CNotificationProcessor", "DispatchToPartners:pthread_create","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}

	// Wait for all threads to be stopped
	for(int i=0; i < m_arrNotificationModel.size(); i++) {
		try{
			pthread_join( arrThread[i], NULL);
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			string strLog;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CNotificationProcessor", "DispatchToPartners:pthread_join","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}
	if (IsAllPartnersNotified(boRecord["_id"]))
	{
		// Set record status to true
		m_pNotificationController->Update(BSON("$set"<<BSON("noti_status"<<1)), QUERY("_id"<<boRecord["_id"]));
	}
}

bool CNotificationProcessor::IsAllPartnersNotified(BSONElement beOID)
{
	BSONObj boTemp;
	for(int i=0; i < m_arrNotificationModel.size(); i++) 
	{
		CNotificationModel* pNotificationModel = m_arrNotificationModel[i];
		boTemp = *pNotificationModel;
		if (!pNotificationModel->IsNotified())
		{
			return false;
		}
		m_pNotificationController->Update(BSON("$set"<<BSON(pNotificationModel->GetPartnerName()<<1)), QUERY("_id"<<beOID));
	}

	return true;
}