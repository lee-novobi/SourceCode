#include "CheckerProcessor.h"
#include "../Common/Common.h"
#include "../Common/DBCommon.h"
#include "../Controller/CollectorController.h"
#include "../Controller/CheckerController.h"
#include "../Model/CollectorModel.h"


CCheckerProcessor::CCheckerProcessor(const string& strFileName)
:CProcessor(strFileName)
{
	m_pCollectorInfoController = new CCollectorController();
	m_pCheckerController = new CCheckerController();
}

CCheckerProcessor::~CCheckerProcessor(void)
{
	if (NULL != m_pCollectorInfoController)
	{
		delete m_pCollectorInfoController;
	}
	if (NULL != m_pCheckerController)
	{
		delete m_pCheckerController;
	}
}

bool CCheckerProcessor::Connect()
{		
	// Register controllers before connecting to database
	RegisterController(m_pCollectorInfoController);
	RegisterController(m_pCheckerController);
	return CProcessor::Connect();
}

void CCheckerProcessor::ProceedCheckDataInfo(const string& strCollectorName)
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
	
		if (pCollectorModel->GetSource() == COLLECTOR_SNS)
		{
			CheckServerStatistic(pCollectorModel);
			CheckHardwareInfo(pCollectorModel);
			sleep(CHECK_INFO_DELAY);
		}
		else
		{
			//Sleep for a while
			sleep(LOAD_INFO_CHANGE_CYCLE);
		}
	}
}

