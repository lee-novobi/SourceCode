#include "VirtualServerInfoUpdateProcessor.h"
#include "../Controller/ServerController.h"
#include "../Controller/ServerInfoChangeController.h"
#include "../Controller/VirtualServerInfoUpdateController.h"
#include "../Controller/ServerHistoryLogController.h"
#include "../Model/ServerInfoChangeModel.h"
#include "../Model/ServerHistoryLogModel.h"

CVirtualServerInfoUpdateProcessor::CVirtualServerInfoUpdateProcessor(const string& strCfgFile)
:CCIInfoUpdateProcessor(strCfgFile)
{
	m_iCIType = CI_TYPE_SERVER;

	m_pCIInfoUpdateController	= new CVirtualServerInfoUpdateController();
	m_pCIInfoChangeController	= new CServerInfoChangeController();
	m_pCMDBController			= new CServerController();
	m_pCIHistoryLogController	= new CServerHistoryLogController();	
	m_pCIHistoryLogModel		= new CServerHistoryLogModel();
	m_pCIInfoChangeModel		= new CServerInfoChangeModel();
}

CVirtualServerInfoUpdateProcessor::~CVirtualServerInfoUpdateProcessor(void)
{
}