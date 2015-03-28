#pragma once
#include "CMDBController.h"

class CCIHistoryLogController : public CCMDBController
{
public:
	CCIHistoryLogController(void);
	~CCIHistoryLogController(void);

	bool InsertHistoryLog(BSONObj boCIInfoChangeRecord, long long lClock);
};
