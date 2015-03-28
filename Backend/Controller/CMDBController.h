#pragma once
#include "MongodbController.h"

class CCMDBController :
	public CMongodbController
{
public:
	CCMDBController(void);
	~CCMDBController(void);

	virtual auto_ptr<DBClientCursor> LoadData();
};
