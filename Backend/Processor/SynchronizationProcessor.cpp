#include "SynchronizationProcessor.h"
#include "../Common/DBCommon.h"
#include "../Controller/CollectorController.h"
#include "../Controller/DivisionController.h"
#include "../Controller/DepartmentController.h"
#include "../Model/CollectorModel.h"


CSynchronizationProcessor::CSynchronizationProcessor(const string& strFileName)
:CProcessor(strFileName)
{
	m_pCollectorInfoController = new CCollectorController();
	m_pDivisionController = new CDivisionController();
	m_pDepartmentController = new CDepartmentController();
}

CSynchronizationProcessor::~CSynchronizationProcessor(void)
{
	if (NULL != m_pCollectorInfoController)
	{
		delete m_pCollectorInfoController;
	}

	if (NULL != m_pDivisionController)
	{
		delete m_pDivisionController;
	}

	if (NULL != m_pDepartmentController)
	{
		delete m_pDepartmentController;
	}
}

bool CSynchronizationProcessor::Connect()
{		
	// Register controllers before connecting to database
	RegisterController(m_pCollectorInfoController);
	RegisterController(m_pDivisionController);
	RegisterController(m_pDepartmentController);
	return CProcessor::Connect();
}

void CSynchronizationProcessor::ProceedSynchronizeInfo(const string& strCollectorName)
{
	if (!Connect())
	{
		return;
	}

	if (!m_pCollectorInfoController->LoadCollectorInfo(strCollectorName))
	{
		return;
	}
	
	while (true)
	{		
		CCollectorModel *pCollectorModel = NULL;
		
		pCollectorModel = m_pCollectorInfoController->GetCollectorInfoByCollectorName(strCollectorName);
	
		if (pCollectorModel->GetSource() == COLLECTOR_MIS)
		{
			//Compare full Division Data
			if (pCollectorModel->GetType() == SYNC_FULL)
			{
				CompareFullDivisionData(pCollectorModel);
				CompareFullDepartmentData(pCollectorModel);
				CompareFullUserData(pCollectorModel);
			}
			else if (pCollectorModel->GetType() == SYNC_CHANGE)
			{
				CompareChangeDivisionData(pCollectorModel);
				CompareChangeDepartmentData(pCollectorModel);
			}

			sleep(SYNC_INFO_DELAY);
		}
		else
		{
			//Sleep for a while
			sleep(LOAD_INFO_CHANGE_CYCLE);
		}
	}
}

