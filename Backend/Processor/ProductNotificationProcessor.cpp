#include "ProductNotificationProcessor.h"
#include "../Model/NotificationModel.h"
#include "../Notification/DCProductNotificationAPI.h"
#include "../Notification/TOMProductNotificationAPI.h"
#include "../Controller/ProductNotificationController.h"

CProductNotificationProcessor::CProductNotificationProcessor(const string& strFileName)
:CNotificationProcessor(strFileName)
{
	m_pNotificationController = new CProductNotificationController();
	m_pFuncStartDispatcher = &StartDispatcher;
}

CProductNotificationProcessor::~CProductNotificationProcessor(void)
{
}

void* CProductNotificationProcessor::StartDispatcher(void *pData)
{
	CNotificationModel* pNotificationModel = static_cast<CNotificationModel*>(pData);
	if (pNotificationModel->GetPartnerName() == PARTNER_DC)
	{
		CDCProductNotificationAPI dcAPI;
		pNotificationModel->SetNotification(dcAPI.Notify(pNotificationModel));
	}
	else if (pNotificationModel->GetPartnerName() == PARTNER_TOM)
	{
		CTOMProductNotificationAPI tomAPI;
		pNotificationModel->SetNotification(tomAPI.Notify(pNotificationModel));
	}
}
