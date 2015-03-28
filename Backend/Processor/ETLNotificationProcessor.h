#pragma once
#include "Processor.h"

class CConfigFile;
class CCMDBController;
class CCIInfoUpdateController;
class CNotificationController;
class CNotificationModel;
class CMongodbModel;

typedef vector<CMongodbModel*> MongodbModelArray;
typedef map<string,MongodbModelArray*> mapKey2BSONObjArrayPtr;
typedef map<string,CMongodbModel*> mapKey2BSONObjPtr;

class CETLNotificationProcessor: public CProcessor
{
public:
	CETLNotificationProcessor(const string& strFileName);
	~CETLNotificationProcessor(void);
	bool ProceedETL();

protected:
	auto_ptr<DBClientCursor> LoadNewCIInfo();
	void PrepareNewInfo(auto_ptr<DBClientCursor> &ptrInfoUpdateCursor);
	void ETLNotification();
	virtual bool Connect();
	void DestroyMapData();
	
protected:	
	mapKey2BSONObjArrayPtr m_mapKey2NewCIInfoArray;
	CCMDBController *m_pCMDBController;
	CCIInfoUpdateController *m_pCIInfoUpdateController;
	CNotificationController *m_pNotificationController;
	CNotificationModel *m_pNotificationModel;
};