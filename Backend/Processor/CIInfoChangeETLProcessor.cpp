#include "CIInfoChangeETLProcessor.h"
#include "../Controller/CIInfoUpdateController.h"
#include "../Controller/CIInfoChangeETLController.h"
#include "../Model/CIInfoChangeETLModel.h"
#include "../Config/ConfigFile.h"
#include "../Common/DBCommon.h"

CCIInfoChangeETLProcessor::CCIInfoChangeETLProcessor(const string& strFileName)
:CProcessor(strFileName)
{	
	m_pCIInfoChangeETLController = new CCIInfoChangeETLController();
	m_pCIInfoChangeETLModel = new CCIInfoChangeETLModel();
	m_pCIInfoUpdateController = new CCIInfoUpdateController();
}

CCIInfoChangeETLProcessor::~CCIInfoChangeETLProcessor(void)
{	
	if (NULL != m_pCIInfoUpdateController)
	{
		delete m_pCIInfoUpdateController;
	}

	if (NULL != m_pCIInfoChangeETLController)
	{
		delete m_pCIInfoChangeETLController;
	}

	if (NULL != m_pCIInfoChangeETLModel)
	{
		delete m_pCIInfoChangeETLModel;
	}
}

auto_ptr<DBClientCursor> CCIInfoChangeETLProcessor::LoadDB()
{
	auto_ptr<DBClientCursor> ptrResultCursor = auto_ptr<DBClientCursor>();
	try
	{
		ptrResultCursor = m_pCIInfoChangeETLController->Find(QUERY("deleted"<<0));
		return ptrResultCursor;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLProcessor", "LoadDB()","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}


bool CCIInfoChangeETLProcessor::ProceedETLRecord(BSONObj &boCIChangeRecord)
{
	BSONObj boUpdateRecord;
	string strTableName;
	try
	{
		boUpdateRecord = m_pCIInfoChangeETLModel->GetUpdateRecord(boCIChangeRecord);
		strTableName = m_pConfigFile->GetTmpTableName(boCIChangeRecord.getStringField("ci_type"));
		if(!m_pCIInfoUpdateController->Insert(strTableName, boUpdateRecord))
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLProcessor", "ProceedETLRecord(BSONObj)", "FAIL:Insert"));
			return false;
		}
		m_pCIInfoChangeETLController->Update(BSON("$set"<<BSON("deleted"<<1)),QUERY("_id"<<boCIChangeRecord["_id"]));
		return true;
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLProcessor", "ProceedETLRecord(BSONObj)","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CCIInfoChangeETLProcessor::Connect()
{	
	// Register controllers before connecting to database
	RegisterController(m_pCIInfoUpdateController);
	RegisterController(m_pCIInfoChangeETLController);

	return CProcessor::Connect();
}

bool CCIInfoChangeETLProcessor::ProceedETL()
{
	cout<<"In ProceedETL."<<endl;
	// Connect to database
	if(!Connect())
	{
		return false;
	}

	cout<<"Database connected."<<endl;

	auto_ptr<DBClientCursor> ptrResultCursor = auto_ptr<DBClientCursor>();
	try
	{
		while(true)
		{
			ptrResultCursor = LoadDB();

			if (ptrResultCursor.get() == NULL)
			{
				continue;
			}

			while(ptrResultCursor->more())
			{
				BSONObj boCIInfoRecord = ptrResultCursor->nextSafe();
				if(!ProceedETLRecord(boCIInfoRecord))
				{
					CUtilities::WriteErrorLog(CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLProcessor", "ProceedETL()", "ProceedETLRecord:FAIL"));
				}
				CUtilities::WriteErrorLog(CUtilities::FormatLog(INFO_MSG, "CCIInfoChangeETLProcessor", "ProceedETL()", "ProceedETLRecord:OID:"+boCIInfoRecord["_id"].toString(false)));
			}
			ptrResultCursor.reset();
		}
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLProcessor", "ProceedETL()","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}