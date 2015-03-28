#include "ETLNotificationProcessor.h"
#include "../Controller/NotificationController.h"
#include "../Controller/CIInfoUpdateController.h"
#include "../Model/NotificationModel.h"
#include "../Config/ConfigFile.h"
#include "../Common/DBCommon.h"

CETLNotificationProcessor::CETLNotificationProcessor(const string& strFileName)
:CProcessor(strFileName)
{
}

CETLNotificationProcessor::~CETLNotificationProcessor(void)
{
	if (NULL != m_pCMDBController)
	{
		delete m_pCMDBController;
	}

	if (NULL != m_pCIInfoUpdateController)
	{
		delete m_pCIInfoUpdateController;
	}

	if (NULL != m_pNotificationController)
	{
		delete m_pNotificationController;
	}
	
	if (NULL != m_pNotificationModel)
	{
		delete m_pNotificationModel;
	}
}

bool CETLNotificationProcessor::Connect()
{
	// Register controllers before connecting to database
	RegisterController(m_pCIInfoUpdateController);
	RegisterController(m_pNotificationController);
	RegisterController(m_pCMDBController);
	return CProcessor::Connect();
}

auto_ptr<DBClientCursor> CETLNotificationProcessor::LoadNewCIInfo() 
{
	auto_ptr<DBClientCursor> ptrResultCursor = auto_ptr<DBClientCursor>();
	try
	{
		ptrResultCursor = m_pCIInfoUpdateController->Find(Query(BSON("notified"<<0)).sort("clock"));
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CETLNotificationProcessor", "LoadNewCIInfo()","Exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}
	return ptrResultCursor;
}

void CETLNotificationProcessor::PrepareNewInfo(auto_ptr<DBClientCursor> &ptrInfoUpdateCursor)
{
	string strKey;
	BSONObj oCIUpdateInfo;
	MongodbModelArray* arrBSONObjModel;

	try
	{
		while (ptrInfoUpdateCursor->more()) 
		{
			oCIUpdateInfo = ptrInfoUpdateCursor->nextSafe();
			strKey	= CUtilities::GetMongoObjId(oCIUpdateInfo.getField("ci_id").toString(false));
			CMongodbModel *oCIUpdateInfoModel = new CMongodbModel(oCIUpdateInfo);

			if (m_mapKey2NewCIInfoArray.find(strKey) != m_mapKey2NewCIInfoArray.end()) 
			{			
				// existed key
				arrBSONObjModel = (MongodbModelArray*) m_mapKey2NewCIInfoArray[strKey];
				arrBSONObjModel->push_back(oCIUpdateInfoModel);
			}
			else 
			{
				// not existed key
				arrBSONObjModel = new MongodbModelArray();
				arrBSONObjModel->push_back(oCIUpdateInfoModel);
				m_mapKey2NewCIInfoArray[strKey] = arrBSONObjModel;
			}
		}
		
		/*BSONObjArray* arrBSONObjModel = m_mapKey2NewCIInfoArray[strKey];
		BSONObj boTemp;
		for(int i =0;arrBSONObjModel->size();i++)
		{
			boTemp = *(*arrBSONObjModel)[i];
			cout << "boTemp:"<< boTemp.toString() << endl << endl;
		}*/
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CETLNotificationProcessor", "PrepareNewInfo(BSONObj)","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}
}

void CETLNotificationProcessor::ETLNotification()
{
	//try
	//{
	//	BSONObj boCIInfo, boNotiRecord;
	//	MongodbModelArray *arrNewCIInfo;
	//	BSONArrayBuilder babKeyArray;
	//	string strKey;
	//	auto_ptr<DBClientCursor> ptrCIInfoCursor;
	//	for(mapKey2BSONObjArrayPtr::iterator mit = m_mapKey2NewCIInfoArray.begin(); mit != m_mapKey2NewCIInfoArray.end(); mit++) 
	//	{
	//		babKeyArray << OID(mit->first);
	//	}
	//	ptrCIInfoCursor = m_pCMDBController->Find(QUERY("_id"<<BSON("$in"<<babKeyArray.arr())));
	//	while(ptrCIInfoCursor->more())
	//	{
	//		boCIInfo = ptrCIInfoCursor->nextSafe();
	//		strKey = CUtilities::GetMongoObjId(boCIInfo.getField("_id").toString(false));;
	//		arrNewCIInfo = m_mapKey2NewCIInfoArray[strKey];
	//		if (arrNewCIInfo->size() > 0)
	//		{
	//			MongodbModelArray::iterator vit = arrNewCIInfo->begin();
	//			while (vit != arrNewCIInfo->end())
	//			{
	//				//CMongodbModel *pNewCIInfoModel = *vit;
	//				//BSONObj boNewCIInfo = *pNewCIInfoModel;
	//				BSONObj boNewCIInfo = *vit;
	//				boNotiRecord = m_pNotificationModel->GetNotificationInfo(boCIInfo, boNewCIInfo);
	//				m_pNotificationController->Insert(boNotiRecord);
	//				m_pCIInfoUpdateController->Update(BSON("$set"<<BSON("notified"<<1)),QUERY("_id"<<boNewCIInfo["_id"]));
	//				vit++;
	//			}
	//		}
	//	}
	//}
	//catch(exception& ex)
	//{	
	//	stringstream strErrorMess;
	//	string strLog;
	//	strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
	//	strLog = CUtilities::FormatLog(ERROR_MSG, "CETLNotificationProcessor", "ETLNotification()","Exception:" + strErrorMess.str());
	//	CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	//}
}

void CETLNotificationProcessor::DestroyMapData()
{
	mapKey2BSONObjArrayPtr::iterator itArray = m_mapKey2NewCIInfoArray.begin();
	while (itArray != m_mapKey2NewCIInfoArray.end())
	{
		/*if (((*itArray).second)->size() > 0)
		{
			MongodbModelArray::iterator itVector = ((*itArray).second)->begin();
			while (itVector != ((*itArray).second)->end())
			{
				delete *itVector;
				itVector++;
			}
		}*/
		delete (*itArray).second;
		itArray++;
	}
	m_mapKey2NewCIInfoArray.clear();
}

bool CETLNotificationProcessor::ProceedETL()
{ 
	if (!Connect())
	{
		return false;
	}

	try
	{
		while (true)
		{
			auto_ptr<DBClientCursor> ptrCIInfoCursor = auto_ptr<DBClientCursor>();
			auto_ptr<DBClientCursor> ptrInfoUpdateCursor = auto_ptr<DBClientCursor>();

			ptrInfoUpdateCursor = LoadNewCIInfo();
			if ((ptrInfoUpdateCursor.get() != NULL) && ptrInfoUpdateCursor->more())
			{
				PrepareNewInfo(ptrInfoUpdateCursor);
				ETLNotification();
				DestroyMapData();
			}
			else
			{
				//Sleep for a while
				sleep(TRACK_CHANGE_INTERVAL);
			}
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCIInfoUpdateProcessor", "ProceedUpdateCIInfo(BSONObj)","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}

	return false;
}
