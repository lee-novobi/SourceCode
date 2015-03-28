#pragma once
#include "../Common/Common.h"

class CNotificationModel;
typedef int (*NotifyInfoChange)(const char*, int);
class CNotificationAPI
{
public:
	CNotificationAPI(void);
	~CNotificationAPI(void);

	bool Notify(CNotificationModel* pData);
protected:
	virtual string Convert2JSON(CNotificationModel* pData) { }
};
