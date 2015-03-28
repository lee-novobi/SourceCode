#include "CheckerController.h"
#include "../Common/DBCommon.h"
#include "../Model/JsonModel.h"
#include "../Model/CollectorModel.h"
#include "../Model/ServerStatisticCheckerModel.h"
#include "../Model/HardwareCheckerModel.h"
#include "../DataChecker/HardwareChecker.h"

CCheckerController::CCheckerController(void)
{
	m_strTableName = "";
}


CCheckerController::~CCheckerController(void)
{
	ClearMapHardwareInfo();
}

void CCheckerController::ClearMapHardwareInfo()
{
	SerialNumber2HardwareInfoMap::iterator it = m_mapSerialNumber2DHardwareInfoMap.begin();
	while(it != m_mapSerialNumber2DHardwareInfoMap.end())
	{
		delete (*it).second;
		it++;
	}
}

CHardwareCheckerModel* CCheckerController::GetHardwareCheckerInfoBySerialNumber(const string& strSerialNumber)
{
	CHardwareCheckerModel* pHardwareCheckerModel = NULL;

	SerialNumber2HardwareInfoMap::iterator it = m_mapSerialNumber2DHardwareInfoMap.find(strSerialNumber);
	
	if (it != m_mapSerialNumber2DHardwareInfoMap.end())
	{
		pHardwareCheckerModel = (*it).second;
	}
	else
	{
		pHardwareCheckerModel = new CHardwareCheckerModel();		
		m_mapSerialNumber2DHardwareInfoMap[strSerialNumber] = pHardwareCheckerModel;
	}

	return pHardwareCheckerModel;
}

/*============= Compare Server Statistic With SnS ========================
 * Function: CompareServerStatisticWithSnS
 * Description: Compare amount server physical & virtual with SnS
 * Return: void
 *========================================================================
 */
void CCheckerController::CompareServerStatisticWithSnS(CCollectorModel *pCoiectorInfo, char* pData)
{
	CJsonModel objJsonModel; 
	string strDataInfo;
	string strDirtyTable;
	string strServerTable;

	int iSnSPhysical = 0;
	int iSnSVirtual = 0;
	
	int iTotalServerU = 0;
	int iTotalServerChassis = 0;
	int iTotalServerVirtual = 0;
	int iTotalServerUnknown = 0;

	BSONObj boCond;

	strDataInfo = pData;
	strDirtyTable = pCoiectorInfo->GetDirtyTableName();
	strServerTable = pCoiectorInfo->GetTableName();
	
	cout << "table:" << strServerTable << endl;
	try
	{
		Json::Value jRootValue;
		jRootValue = objJsonModel.parseValueRootJson(strDataInfo);
		
		if (!jRootValue.isNull())
		{
			if (!jRootValue["Physical"].isNull())
				iSnSPhysical = jRootValue["Physical"].asInt();
			if (!jRootValue["Virtual"].isNull())
				iSnSVirtual = jRootValue["Virtual"].asInt();
			
			cout << "sns physical:" << iSnSPhysical << endl;
			cout << "sns virtual:" << iSnSVirtual << endl;

			//Count server U
			boCond = BSON("server_type" << SERVER_U);
			iTotalServerU = (int)Count(strServerTable, boCond);
			cout << "Server U:" << iTotalServerU << endl;

			//Count Server Chassis
			boCond = BSON("server_type" << SERVER_CHASSIS);
			iTotalServerChassis = (int)Count(strServerTable, boCond);
			cout << "Server Chassis:" << iTotalServerChassis << endl;

			//Count Server Virtual
			boCond = BSON("server_type" << SERVER_VIRTUAL);
			iTotalServerVirtual = (int)Count(strServerTable, boCond);
			cout << "Server Virtual:" << iTotalServerVirtual << endl;

			//Count Server Unknown
			boCond = BSON("server_type" << UNKNOWN);
			iTotalServerUnknown = (int)Count(strServerTable, boCond);
			cout << "Server Unknown:" << iTotalServerUnknown << endl;

			if ((iSnSVirtual != iTotalServerVirtual) || 
				(iSnSPhysical != (iTotalServerU + iTotalServerChassis + iTotalServerUnknown)))
			{
				time_t tDate;
				tDate = CUtilities::GetDateAgo(0);

				CServerStatisticCheckerModel oServStatisticModel;
				oServStatisticModel.SetSnSVirtual(iSnSVirtual);
				oServStatisticModel.SetSnSPhysical(iSnSPhysical);
				oServStatisticModel.SetCMDBVirtual(iTotalServerVirtual);
				oServStatisticModel.SetCMDBU(iTotalServerU);
				oServStatisticModel.SetCMDBChassis(iTotalServerChassis);
				oServStatisticModel.SetCMDBUnknown(iTotalServerUnknown);
				oServStatisticModel.SetClock(tDate);
				SaveServerStatisticChecker(strDirtyTable, oServStatisticModel);
			}
		}
	}
	catch(exception& ex)
	{	
		stringstream ssErrorMsg;
		string strLog;
		ssErrorMsg << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCheckerController", "CompareServerStatisticWithSnS","exception:" + ssErrorMsg.str());
		CUtilities::WriteErrorLog(strLog);
	}
}


/*============= Save Server Statistic which was checked ========================
 * Function: SaveServerStatisticChecker
 * Description: Save Server Statistic which was check with SnS
 * Return: void
 *========================================================================
 */
void CCheckerController::SaveServerStatisticChecker(const string& strTableName, CServerStatisticCheckerModel objServStatistic)
{
	BSONObj boServStatisticInfo = objServStatistic.GetServerStatisticCheckerInfo();
	BSONObj boCondition;

	Remove(strTableName, boCondition);
	cout << "checker table:" << strTableName << endl;
	Insert(strTableName, boServStatisticInfo, boCondition);
}

/*============= Get list serial number physical========================
 * Function: GetListSerialNumber
 * Description: Get list serial number of physical server
 * Return: vector<char*>
 *========================================================================
 */
void CCheckerController::LoadCMDBHardwareInfo(auto_ptr<DBClientCursor> &ptrServerResultCursor)
{
	CHardwareCheckerModel *pHardwareCheckerModel;

	BSONArrayBuilder baServerType;
	baServerType.append(SERVER_U);
	baServerType.append(SERVER_CHASSIS);
	baServerType.append(UNKNOWN);
	Query qCondition = QUERY("server_type" << BSON("$in" << baServerType.arr()));
					
	ptrServerResultCursor = Find(TBL_SERVER, qCondition);
	BSONObj oServerInfo;
	string strSerialNumber;
	string strCpuCfg;
	string strServerModelCfg;

	try
	{
		if (ptrServerResultCursor->more())
		{
			while(ptrServerResultCursor->more())
			{
				strSerialNumber = strCpuCfg = strServerModelCfg = "";
				oServerInfo = ptrServerResultCursor->nextSafe();

				strSerialNumber = oServerInfo.getStringField("code");
				strCpuCfg = oServerInfo.getStringField("cpu_config");
				strServerModelCfg = oServerInfo.getStringField("server_model");

				pHardwareCheckerModel = GetHardwareCheckerInfoBySerialNumber(strSerialNumber);
				pHardwareCheckerModel->SetSerialNumber(strSerialNumber);
				pHardwareCheckerModel->SetCmdbCpuInfo(strCpuCfg);
				pHardwareCheckerModel->SetCmdbModelInfo(strServerModelCfg);
			}
		}
	}
	catch(exception& ex)
	{	
		stringstream ssErrorMsg;
		string strLog;
		ssErrorMsg << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCheckerController", "LoadCMDBHardwareInfo","exception:" + ssErrorMsg.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);
	}
}

/*============= Compare hardware info with SnS ========================
 * Function: CompareHardwareWithSnS
 * Description: Compare hardware info with SnS
 * Return: void
 *========================================================================
 */
void CCheckerController::CompareHardwareWithSnS(CCollectorModel *pCollectorInfo, CHardwareChecker *pHardwareChecker)
{
	auto_ptr<DBClientCursor> ptrServerResultCursor;
	LoadCMDBHardwareInfo(ptrServerResultCursor);
	
	string strSerialNumber;
	string strCpuInfo;
	string strServerModelInfo;
	
	string strSnSCpuInfo;
	string strSnSModelInfo;
	string strDirtyTable;

	CHardwareCheckerModel *pHardwareCheckerModel;

	strDirtyTable = pCollectorInfo->GetDirtyTableName();
	
	try
	{
		BSONObj boCondition;
		Remove(strDirtyTable, boCondition);

		for( SerialNumber2HardwareInfoMap::iterator it = m_mapSerialNumber2DHardwareInfoMap.begin(); it!=m_mapSerialNumber2DHardwareInfoMap.end(); ++it)
		{
			char *pData;
			pData = NULL;
			strSerialNumber = strCpuInfo = strServerModelInfo = "";
			
			strSerialNumber = (*it).first;
			pHardwareCheckerModel = (*it).second;
		
			strCpuInfo = pHardwareCheckerModel->GetCmdbCpuInfo();
			strServerModelInfo = pHardwareCheckerModel->GetCmdbModelInfo();
			
			//Call API
			pHardwareChecker->SetSerialNumber((char*)strSerialNumber.c_str());
			pData = pHardwareChecker->CallSnSService(pCollectorInfo);
			
			if (NULL != pData)
			{
				//cout << "Data" << pData << endl;
				strSnSCpuInfo = GetSnSCpuInfo(pData);
				strSnSModelInfo = GetSnSServerModelInfo(pData);

				if (!CUtilities::IsMatch2String(strCpuInfo, strSnSCpuInfo) ||
					!CUtilities::IsMatch2String(strServerModelInfo, strSnSModelInfo))
				{
					pHardwareCheckerModel->SetIsMatch(0);
				}
				else
				{
					pHardwareCheckerModel->SetIsMatch(1);
				}
				pHardwareCheckerModel->SetSnSCpuInfo(strSnSCpuInfo);
				pHardwareCheckerModel->SetSnSModelInfo(strSnSModelInfo);
				SaveHardwareCheckerInfo(strDirtyTable, pHardwareCheckerModel);
				
				delete pData;
			}
		}
		ptrServerResultCursor.reset();
	}
	catch(exception& ex)
	{	
		stringstream ssErrorMsg;
		string strLog;
		ssErrorMsg << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCheckerController", "LoadCMDBHardwareInfo","exception:" + ssErrorMsg.str());
		CUtilities::WriteErrorLog(strLog);
	}
	cout << "Finished" << endl;
}

/*============= Get SnS Cpu info from API ========================
 * Function: GetSnSCpuInfo
 * Description: Get SnS Cpu Info from API
 * Return: string
 *========================================================================
 */
string CCheckerController::GetSnSCpuInfo(char* pData)
{
	string strCpuInfo = "";

	CJsonModel objJsonModel; 
	string strDataInfo;
	int iTotalCPU;

	strDataInfo = pData;

	Json::Value jRootValue;
	jRootValue = objJsonModel.parseValueRootJson(strDataInfo);
	
	if (!jRootValue.isNull())
	{
		try
		{
			if (!jRootValue["Data"]["Processor"].isNull() && 
				jRootValue["Data"]["Processor"].isArray())
			{
				Json::Value joProcessor;
				joProcessor = jRootValue["Data"]["Processor"];
				
				//cout << "processor:" << joProcessor << endl;
				iTotalCPU = joProcessor.size();

				for(Json::Value::UInt index = 0 ; index < iTotalCPU; index ++)
				{
					if (!joProcessor[index]["name"].isNull())
					{
						strCpuInfo = joProcessor[index]["name"].asString();
					}
				}
			}
		}
		catch(exception& ex)
		{
		}
	}

	if (strCpuInfo != "")
	{
		strCpuInfo = strCpuInfo + " x " + CUtilities::ConvertIntToString(iTotalCPU);
	}

	return strCpuInfo;
}

/*============= Get SnS Cpu info from API ========================
 * Function: GetSnSServerModelInfo
 * Description: Get SnS Server Model Info from API
 * Return: string
 *========================================================================
 */
string CCheckerController::GetSnSServerModelInfo(char* pData)
{
	string strServerModel = "";

	CJsonModel objJsonModel; 
	string strDataInfo;

	strDataInfo = pData;

	Json::Value jRootValue;
	jRootValue = objJsonModel.parseValueRootJson(strDataInfo);
	
	if (!jRootValue.isNull())
	{
		try
		{
			if (!jRootValue["Data"]["General"]["manufacture"].isNull())
				strServerModel = jRootValue["Data"]["General"]["manufacture"].asString();

			if (!jRootValue["Data"]["General"]["model"].isNull())
			{
				if (strServerModel != "")
					strServerModel = strServerModel + " " + jRootValue["Data"]["General"]["model"].asString();
				else
					strServerModel = jRootValue["Data"]["General"]["model"].asString();
			}
		}
		catch(exception& ex)
		{
		}
	}
	return strServerModel;
}

void CCheckerController::SaveHardwareCheckerInfo(const string& strTableName, CHardwareCheckerModel *pHardwareCheckerModel)
{
	BSONObj boHardwareInfo;
	BSONObj boCondition;

	boHardwareInfo = pHardwareCheckerModel->GetHardwareCheckerInfo();
	boCondition = BSON("serial_number" << pHardwareCheckerModel->GetSerialNumber());
	
	//cout << "Data:" << boHardwareInfo.toString() << endl;
	Insert(strTableName, boHardwareInfo, boCondition);
}