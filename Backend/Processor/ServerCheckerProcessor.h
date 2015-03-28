#pragma once
#include "CheckerProcessor.h"

class CCollectorModel;
class CServerChecker;

class CServerCheckerProcessor :
	public CCheckerProcessor
{
public:
	CServerCheckerProcessor(const string& strCfgFile);
	~CServerCheckerProcessor(void);
	void CheckServerStatistic(CCollectorModel* pCollectorInfo);
protected:
	CServerChecker *m_pServerChecker;
};
