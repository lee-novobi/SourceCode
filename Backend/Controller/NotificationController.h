#pragma once
#include "CMDBController.h"

class CNotificationController :
	public CCMDBController
{
public:
	CNotificationController(void);
	~CNotificationController(void);

	bool SetStatus();
};
