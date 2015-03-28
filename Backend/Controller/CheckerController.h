#pragma once
#include "CMDBController.h"

class CCollectorModel;
class CServerStatisticCheckerModel;
class CHardwareCheckerModel;
class CHardwareChecker;

typedef map<string, CHardwareCheckerModel*> SerialNumber2HardwareInfoMap;

class CCheckerController :
	public CCMDBController
{
public:
	CCheckerController(void);
	~CCheckerController(void);
	
	//----------- Compare Server Statistic ---------//
	void CompareServerStatisticWithSnS(CCollectorModel *pCollectorInfo, char* pData);
	void SaveServerStatisticChecker(const string& strTableName, CServerStatisticCheckerModel objServStatistic);
	//----------------------------------------------//

	//----------- Compare Hardware Info ------------//
	void CompareHardwareWithSnS(CCollectorModel *pCollectorInfo, CHardwareChecker *pHardwareChecker);
	void LoadCMDBHardwareInfo(auto_ptr<DBClientCursor> &ptrServerResultCursor);
	void ClearMapHardwareInfo();
	CHardwareCheckerModel* GetHardwareCheckerInfoBySerialNumber(const string& strSerialNumber);
	string GetSnSCpuInfo(char* pData);
	string GetSnSServerModelInfo(char* pData);
	void SaveHardwareCheckerInfo(const string& strTableName, CHardwareCheckerModel *pHardwareCheckerModel);
	//----------------------------------------------//
protected:
	CHardwareCheckerModel* GetHardwareCheckerInfoBySerialNumber(char* pSerialNumber);
protected:
	SerialNumber2HardwareInfoMap m_mapSerialNumber2DHardwareInfoMap;
};
