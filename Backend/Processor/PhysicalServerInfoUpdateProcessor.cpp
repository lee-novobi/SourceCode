#include "PhysicalServerInfoUpdateProcessor.h"
#include "../Controller/ServerController.h"
#include "../Controller/ServerInfoChangeController.h"
#include "../Controller/PhysicalServerInfoUpdateController.h"
#include "../Controller/ServerHistoryLogController.h"
#include "../Controller/PhysicalServerNotificationController.h"
#include "../Model/ServerInfoChangeModel.h"
#include "../Model/CIHistoryLogModel.h"
#include "../Model/ServerHistoryLogModel.h"
#include "../Model/ServerNotificationModel.h"

CPhysicalServerInfoUpdateProcessor::CPhysicalServerInfoUpdateProcessor(const string& strCfgFile)
:CCIInfoUpdateProcessor(strCfgFile)
{
	m_iCIType = CI_TYPE_SERVER;

	m_pCIInfoUpdateController	= new CPhysicalServerInfoUpdateController();
	m_pCIInfoChangeController	= new CServerInfoChangeController();
	m_pCMDBController			= new CServerController();
	m_pCIHistoryLogController	= new CServerHistoryLogController();	
	m_pNotificationController	= new CPhysicalServerNotificationController();
	m_pCIHistoryLogModel		= new CServerHistoryLogModel();
	m_pCIInfoChangeModel		= new CServerInfoChangeModel();
	m_pNotificationModel		= new CServerNotificationModel();
	m_bIsNotification = true;
}

CPhysicalServerInfoUpdateProcessor::~CPhysicalServerInfoUpdateProcessor(void)
{	
}