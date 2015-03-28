#pragma once
#include "NotificationAPI.h"

class CDCProductNotificationAPI :
	public CNotificationAPI
{
public:
	CDCProductNotificationAPI(void);
	~CDCProductNotificationAPI(void);
protected:
	string Convert2JSON(CNotificationModel* pData);
};
