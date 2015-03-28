#pragma once
#include "../Common/Common.h"

class CCIInfoIndexModel;
class CCIInfoIndexController;
class CConfigFile;
class CCIIndexPoolerModel;

typedef vector<CCIInfoIndexModel*> CIInfoIndexModelArray;
class CCIIndexPooler
{
public:
	CCIIndexPooler(const string& strCfgFile);
	~CCIIndexPooler(void);

	void ProceedInfo(CCIIndexPoolerModel* pCIIndexPoolerModel);

protected:	
	virtual bool Connect();

	bool ProceedIndex(CCIInfoIndexModel* pCIInfoIndexModel);
	bool DeleteInfo(CCIInfoIndexModel* pCIInfoIndexModel);
	bool IsIndexExisted(CCIInfoIndexModel* pCIInfoIndexModel, string strValue);
	bool AddIndex(CCIInfoIndexModel* pCIInfoIndexModel);
	bool DeleteIndex(CCIInfoIndexModel* pCIInfoIndexModel);
	bool UpdateIndex(CCIInfoIndexModel* pCIInfoIndexModel);	

protected:
	CCIInfoIndexController *m_pCIInfoIndexController;
	CConfigFile *m_pConfigFile;	
};
