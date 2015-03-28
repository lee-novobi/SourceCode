#pragma once
#include "MongodbModel.h"

class CCIInfoChangeETLModel:
	public CMongodbModel
{
public:
	CCIInfoChangeETLModel(void);
	~CCIInfoChangeETLModel(void);
	BSONObj GetUpdateRecord(BSONObj boCIChangeRecord);
};