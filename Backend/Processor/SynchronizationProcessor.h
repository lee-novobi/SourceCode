#pragma once
#include "Processor.h"

class CCollectorController;
class CDivisionController;
class CDepartmentController;
class CCollectorModel;

class CSynchronizationProcessor :
	public CProcessor
{
public:
	CSynchronizationProcessor(const string& strFileName);
	~CSynchronizationProcessor(void);

	void ProceedSynchronizeInfo(const string& strCollectorName);
	virtual void CompareFullDivisionData(CCollectorModel* pCollectorInfo){};
	virtual void CompareChangeDivisionData(CCollectorModel* pCollectorInfo){};
	virtual void CompareFullDepartmentData(CCollectorModel* pCollectorInfo){};
	virtual void CompareChangeDepartmentData(CCollectorModel* pCollectorInfo){};
	virtual void CompareFullUserData(CCollectorModel* pCollectorInfo){};
protected:
	bool Connect();	
protected:
	CCollectorController* m_pCollectorInfoController;
	CDivisionController* m_pDivisionController;
	CDepartmentController* m_pDepartmentController;
};
