#pragma once
#include "NotificationAPI.h"

class CDCServerNotificationAPI :
	public CNotificationAPI
{
public:
	CDCServerNotificationAPI(void);
	~CDCServerNotificationAPI(void);
protected:
	string Convert2JSON(CNotificationModel* pData);
};
