#pragma once
#include "Processor.h"

class CCollectorController;
class CCheckerController;

class CCollectorModel;

class CCheckerProcessor :
	public CProcessor
{
public:
	CCheckerProcessor(const string& strFileName);
	~CCheckerProcessor(void);

	void ProceedCheckDataInfo(const string& strCollectorName);
	virtual void CheckServerStatistic(CCollectorModel* pCollectorInfo){};
	virtual void CheckHardwareInfo(CCollectorModel* pCollectorInfo){};
protected:
	bool Connect();	
protected:
	CCollectorController* m_pCollectorInfoController;
	CCheckerController* m_pCheckerController;
};
