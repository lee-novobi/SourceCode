#pragma once
#include "../Common/Common.h"
#include "../Model/MongodbModel.h"
#include "mongo/client/dbclient.h"
using namespace mongo;

class CConfigFile;
class CMongodbController;
class CMongodbModel;

typedef vector<CMongodbModel*> MongodbModelArray;
typedef vector<CMongodbController*> MongodbControllerArray;
class CProcessor
{
public:
	CProcessor(const string& strFileName);
	~CProcessor(void);
protected:
	void RegisterController(CMongodbController* pController);
	virtual bool Connect();
protected:
	CConfigFile* m_pConfigFile;
	MongodbControllerArray m_arrMongodbController;
};
