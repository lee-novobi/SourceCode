#include "PhysicalServerETLNotificationProcessor.h"
#include "../Controller/ServerController.h"
#include "../Controller/PhysicalServerInfoUpdateController.h"
#include "../Controller/PhysicalServerNotificationController.h"
#include "../Model/ServerNotificationModel.h"

CPhysicalServerETLNotificationProcessor::CPhysicalServerETLNotificationProcessor(const string& strFileName)
:CETLNotificationProcessor(strFileName)
{
	m_pCMDBController = new CServerController();
	m_pCIInfoUpdateController = new CPhysicalServerInfoUpdateController();
	m_pNotificationController = new CPhysicalServerNotificationController();
	m_pNotificationModel = new CServerNotificationModel();
}

CPhysicalServerETLNotificationProcessor::~CPhysicalServerETLNotificationProcessor(void)
{
}