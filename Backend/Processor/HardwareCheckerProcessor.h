#pragma once
#include "CheckerProcessor.h"

class CCollectorModel;
class CHardwareChecker;


class CHardwareCheckerProcessor :
	public CCheckerProcessor
{
public:
	CHardwareCheckerProcessor(const string& strCfgFile);
	~CHardwareCheckerProcessor(void);
	void CheckHardwareInfo(CCollectorModel* pCollectorInfo);
protected:
	CHardwareChecker *m_pHardwareChecker;
};
