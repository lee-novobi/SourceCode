#include "ServerCheckerProcessor.h"
#include "../Controller/CollectorController.h"
#include "../Controller/CheckerController.h"
#include "../Model/CollectorModel.h"
#include "../DataChecker/ServerChecker.h"

CServerCheckerProcessor::CServerCheckerProcessor(const string& strCfgFile)
:CCheckerProcessor(strCfgFile)
{	
	m_pServerChecker = new CServerChecker();
}

CServerCheckerProcessor::~CServerCheckerProcessor(void)
{	
	if (NULL != m_pServerChecker)
		delete m_pServerChecker;
}

void CServerCheckerProcessor::CheckServerStatistic(CCollectorModel* pCollectorInfo)
{
	char *pData;
	pData = NULL;
	
	pData = m_pServerChecker->CallSnSService(pCollectorInfo);
	if (NULL != pData)
	{
		cout << "Data:" << pData << endl;
		m_pCheckerController->CompareServerStatisticWithSnS(pCollectorInfo, pData);
		delete pData;
	}
}