#pragma once
#include "CMDBController.h"

class CCollectorModel;
class CDivisionModel;
class CDivisionDefenseInfoModel;

typedef map<string, CDivisionDefenseInfoModel*> HRCode2DivisionDefenseInfoMap;
typedef map<string, CDivisionModel*> HRCode2DivisionInfoMap;

class CDivisionController :
	public CCMDBController
{
public:
	CDivisionController(void);
	~CDivisionController(void);

	void CompareFullData(CCollectorModel* pCollectorInfo, char *pData);
	void CompareChangeData(CCollectorModel* pCollectorInfo, char *pData);
	bool IsMatchDivisionAlias(string strOrgCode);

	void PushDirtyDivisionOrgChart(string strDirtyTable, string strOrgCode, string strOrgId, int iFlag);
	void SaveDivisionDefenseInfo(const string& strTableName, CDivisionDefenseInfoModel *pDivisionDefenseModel);
	void SaveDivisionInfo(CDivisionModel *pDivisionModel);

protected:
	CDivisionDefenseInfoModel* GetDivisionDefenseInfoByHRCode(const string& strHRCode);
	CDivisionModel* GetDivisionInfoByHRCode(const string& strHRCode);
	void ClearMapDivisionDefenseInfo();
	void ClearMapDivisionInfo();
protected:
	HRCode2DivisionDefenseInfoMap m_mapHRCode2DivisionDefenseInfo;
	HRCode2DivisionInfoMap m_mapHRCode2DivisionInfoMap;
};