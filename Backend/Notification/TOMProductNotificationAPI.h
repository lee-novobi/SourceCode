#pragma once
#include "NotificationAPI.h"

class CTOMProductNotificationAPI :
	public CNotificationAPI
{
public:
	CTOMProductNotificationAPI(void);
	~CTOMProductNotificationAPI(void);
protected:
	string Convert2JSON(CNotificationModel* pData);
};
