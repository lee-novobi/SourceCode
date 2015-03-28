#include "HardwareCheckerProcessor.h"
#include "../Controller/CollectorController.h"
#include "../Controller/CheckerController.h"
#include "../Model/CollectorModel.h"
#include "../DataChecker/HardwareChecker.h"

CHardwareCheckerProcessor::CHardwareCheckerProcessor(const string& strCfgFile)
:CCheckerProcessor(strCfgFile)
{	
	m_pHardwareChecker = new CHardwareChecker();
}

CHardwareCheckerProcessor::~CHardwareCheckerProcessor(void)
{	
	if (NULL != m_pHardwareChecker)
		delete m_pHardwareChecker;
}

void CHardwareCheckerProcessor::CheckHardwareInfo(CCollectorModel* pCollectorInfo)
{
	m_pCheckerController->CompareHardwareWithSnS(pCollectorInfo, m_pHardwareChecker);
}