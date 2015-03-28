#include "MongodbController.h"
#include "../Common/DBCommon.h"
ConnectInfo g_CMongoInfo;

CMongodbController::CMongodbController(void)
{
	m_bIsConnected = false;
	m_strTableName = "";
	new(&m_connDB) DBClientConnection(true);
	m_pRSConnDB = NULL;
	m_bIsReplicaSetUsed = false;
	m_nReadReference = 0;
}

CMongodbController::~CMongodbController(void)
{
	if (NULL != m_pRSConnDB)
	{
		delete m_pRSConnDB;
	}
}

bool CMongodbController::Connect(ConnectInfo CInfo)
{
	string strLog;
	string strErrMsg;
	ConnectionString oCS;
	bool bResult = true;

	if (m_bIsConnected)
	{
		return true;
	}

	m_bIsReplicaSetUsed = CInfo.bIsReplicateSetUsed;
	if (CInfo.strReadReferenceOption == READ_REFERENCE_SECONDARY)
	{
		m_nReadReference = QueryOption_SlaveOk;
	}
	try
	{		
		if (m_bIsReplicaSetUsed)
		{
			// Connect to database use replica set
			oCS = ConnectionString::parse(CInfo.strHost , strErrMsg);
			if (!oCS.isValid() ) 
			{				
				bResult = false;
			}
			else
			{
				m_pRSConnDB = static_cast<DBClientReplicaSet*>(oCS.connect(strErrMsg, 0));
				if (!m_pRSConnDB) {
					bResult = false;
				}
			}
		}
		else
		{
			// Connect to single databse server or shard manager
			bResult = m_connDB.connect(CInfo.strHost, strErrMsg);
		}

		if(!bResult)
		{			
			m_bIsConnected = false;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Connect","connect->FAIL:" + CInfo.strHost + "|" + strErrMsg);		
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);

			return false;
		}
	}
	catch(SocketException& ex)
	{
		CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
	}
	catch(DBException& ex)
	{	
		m_bIsConnected = false;
		stringstream strErrorMess;
		strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Connect","Connect.exception:" + CInfo.strHost + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		return false;
	}
	
	try
	{
		strErrMsg.clear();
		if (m_bIsReplicaSetUsed)
		{
			bResult = m_pRSConnDB->auth(CInfo.strSource, CInfo.strUser, CInfo.strPass, strErrMsg);
		}
		else
		{
			bResult = m_connDB.auth(CInfo.strSource, CInfo.strUser, CInfo.strPass, strErrMsg);
		}

		if (!bResult)
		{
			m_bIsConnected = false;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Connect","auth->FAIL:" + CInfo.strSource + "|" + CInfo.strUser + "|" + CInfo.strPass + "|" + strErrMsg);
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
			return false;
		}

		m_bIsConnected = true;		
		g_CMongoInfo = CInfo;
		m_strNameSpace = CInfo.strSource + "." + m_strTableName; 
		return true;
	}
	catch(SocketException& ex)
	{
		CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
	}
	catch(DBException& ex)
	{	
		m_bIsConnected = false;
		stringstream strErrorMess;
		strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Connect","Authen.exception:" + CInfo.strSource + "|" + CInfo.strUser + "|" + CInfo.strPass + "|" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}

	return false;
}

auto_ptr<DBClientCursor> CMongodbController::Find(Query queryCondition)
{	
	auto_ptr<DBClientCursor> ptrResultCursor = auto_ptr<DBClientCursor>();
	if(m_bIsConnected)
	{
		try
		{
			if (m_bIsReplicaSetUsed)
			{
				ptrResultCursor = m_pRSConnDB->query(m_strNameSpace, queryCondition, m_nReadReference);
			}
			else
			{
				ptrResultCursor = m_connDB.query(m_strNameSpace, queryCondition);
			}			
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{	
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Find","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}
	return ptrResultCursor;
}

auto_ptr<DBClientCursor> CMongodbController::Find(string strTable, Query queryCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	auto_ptr<DBClientCursor> ptrResultCursor = Find(queryCondition);

	m_strNameSpace = strOldNameSpace;

	return ptrResultCursor;	
}

bool CMongodbController::Insert(string strTable, BSONObj bsonRecord, BSONObj bsonCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = Insert(bsonRecord, bsonCondition);

	m_strNameSpace = strOldNameSpace;

	return bResult;
}

bool CMongodbController::Insert(BSONObj bsonRecord, BSONObj bsonCondition)
{
	if(m_bIsConnected)
	{
		try
		{
			if(!IsExisted(Query(bsonCondition)) || bsonCondition.isEmpty())
			{											
				if (m_bIsReplicaSetUsed)
				{
					m_pRSConnDB->insert(m_strNameSpace, bsonRecord);					
				}
				else
				{
					m_connDB.insert(m_strNameSpace, bsonRecord);					
				}								
				
				return true;				
			}
			else
			{
				return Update(BSON("$set" << bsonRecord), Query(bsonCondition));
			}
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{			
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Insert","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}

	return false;
}

bool CMongodbController::InsertPartition(BSONObj bsonRecord, BSONObj bsonKeysIndex, string strSuffix, BSONObj bsonCondition)
{
	string strLog;
	if(m_bIsConnected)
	{
		if(!IsExisted(Query(bsonCondition)))
		{
			try
			{
				if (m_bIsReplicaSetUsed)
				{
					m_pRSConnDB->insert(m_strNameSpace + "_" + strSuffix, bsonRecord);
					if(!bsonKeysIndex.isEmpty())
					{
						m_pRSConnDB->ensureIndex(m_strNameSpace + "_" + strSuffix, bsonKeysIndex);
					}
				}
				else
				{
					m_connDB.insert(m_strNameSpace + "_" + strSuffix, bsonRecord);
					if(!bsonKeysIndex.isEmpty())
					{
						m_connDB.ensureIndex(m_strNameSpace + "_" + strSuffix, bsonKeysIndex);
					}
				}
				
				return true;
			}
			catch(SocketException& ex)
			{
				CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
			}
			catch(DBException& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
				strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "InsertPartition","exception:" + strErrorMess.str());
				CUtilities::WriteErrorLog(ERROR_MSG, strLog);
			}
		}
		else
		{
			return Update(bsonRecord, Query(bsonCondition));
		}
	}
	return false;
}

bool CMongodbController::BulkInsert(string strTable, vector<BSONObj> vboRecord)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = BulkInsert(vboRecord);

	m_strNameSpace = strOldNameSpace;

	return bResult;
}

bool CMongodbController::BulkInsert(vector<BSONObj> vboRecord)
{
	if(m_bIsConnected)
	{
		try
		{								
			if (m_bIsReplicaSetUsed)
			{
				m_pRSConnDB->insert(m_strNameSpace, vboRecord);					
			}
			else
			{
				m_connDB.insert(m_strNameSpace, vboRecord);					
			}								
				
			return true;				
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{			
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Insert","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}

	return false;
}

bool CMongodbController::Update(BSONObj bsonRecord, Query queryCondition)
{
	if(m_bIsConnected)
	{
		try
		{
			if (m_bIsReplicaSetUsed)
			{
				m_pRSConnDB->update(m_strNameSpace, queryCondition, bsonRecord, false, true);
			}
			else
			{
				m_connDB.update(m_strNameSpace, queryCondition, bsonRecord, false, true);
			}
			return true;
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{	
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Update","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}
	return false;
	
}

bool CMongodbController::Update(string strTable, BSONObj bsonRecord, Query queryCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = Update(bsonRecord, queryCondition);
	
	m_strNameSpace = strOldNameSpace;

	return bResult;
}

bool CMongodbController::Upsert(BSONObj bsonRecord, Query queryCondition)
{
	if(m_bIsConnected)
	{		
		try
		{			
			if (m_bIsReplicaSetUsed)
			{
				m_pRSConnDB->update(m_strNameSpace, queryCondition, bsonRecord, true, true);				
			}
			else
			{
				m_connDB.update(m_strNameSpace, queryCondition, bsonRecord, true, true);				
			}			
			
			return true;
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{		
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Update","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}

	return false;	
}

bool CMongodbController::Upsert(string strTable, BSONObj bsonRecord, Query queryCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = Upsert(bsonRecord, queryCondition);

	m_strNameSpace = strOldNameSpace;
	
	return bResult;
}

bool CMongodbController::Remove(Query queryCondition)
{
	if(m_bIsConnected)
	{		
		try
		{			
			if (m_bIsReplicaSetUsed)
			{
				m_pRSConnDB->remove(m_strNameSpace, queryCondition);				
			}
			else
			{
				m_connDB.remove(m_strNameSpace, queryCondition);				
			}
			
			return true;
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{		
			string strLog;
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Remove","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}
	return false;
}

bool CMongodbController::Remove(string strTable, Query queryCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = Remove(queryCondition);

	m_strNameSpace = strOldNameSpace;
	
	return bResult;
}

long long CMongodbController::Count(BSONObj bsonCondition)
{
	string strLog;
	long long lCount = 0;

	if (m_bIsConnected)
	{
		try
		{
			if (m_bIsReplicaSetUsed)
			{
				lCount = m_pRSConnDB->count(m_strNameSpace, bsonCondition);
			}
			else
			{
				lCount = m_connDB.count(m_strNameSpace, bsonCondition);
			}
			
		}
		catch(SocketException& ex)
		{
			CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
		}
		catch(DBException& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Count","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(ERROR_MSG, strLog);
		}
	}

	return lCount;
}

long long CMongodbController::Count(string strTable, BSONObj bsonCondition)
{
	long long lCount = 0;

	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	
	lCount = Count(bsonCondition);
	m_strNameSpace = strOldNameSpace;
	
	return lCount;
}

bool CMongodbController::IsExisted(Query queryCondition)
{
	string strLog;
	auto_ptr<DBClientCursor> ptrTmp = auto_ptr<DBClientCursor>();
	bool bResult = false;

	try{
		if (m_bIsReplicaSetUsed)
		{
			ptrTmp = m_pRSConnDB->query(m_strNameSpace, queryCondition, 1, m_nReadReference);
		}
		else
		{
			ptrTmp = m_connDB.query(m_strNameSpace, queryCondition,1);
		}
		
		if (ptrTmp.get() == NULL)
		{
			return false;
		}

		if(ptrTmp->more())
		{
			bResult = true;		
		}		
	}
	catch(SocketException& ex)
	{
		CUtilities::WriteErrorLog(ERROR_MSG, MSG_SOCKET_ERROR);
	}
	catch(DBException& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess << ex.toString() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CMongodbController", "Count","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}

	return bResult;
}

bool CMongodbController::IsExisted(string strTable, Query queryCondition)
{
	string strOldNameSpace = m_strNameSpace;
	m_strNameSpace = g_CMongoInfo.strSource + "." + strTable;
	bool bResult = IsExisted(queryCondition);

	m_strNameSpace = strOldNameSpace;
	
	return bResult;
}