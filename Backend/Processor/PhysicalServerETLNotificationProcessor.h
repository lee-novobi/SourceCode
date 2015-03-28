#pragma once
#include "ETLNotificationProcessor.h"

class CPhysicalServerETLNotificationProcessor :
	public CETLNotificationProcessor
{
public:
	CPhysicalServerETLNotificationProcessor(const string& strFileName);
	~CPhysicalServerETLNotificationProcessor(void);
};
