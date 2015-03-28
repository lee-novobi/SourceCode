#pragma once
#include "Processor.h"

class CCIInfoUpdateController;
class CCIInfoChangeETLController;
class CCIInfoChangeETLModel;
class CCIInfoChangeETLProcessor: public CProcessor
{
public:
	CCIInfoChangeETLProcessor(const string& strFileName);
	~CCIInfoChangeETLProcessor(void);
	bool ProceedETL();
protected:
	virtual auto_ptr<DBClientCursor> LoadDB();
	virtual bool ProceedETLRecord(BSONObj &boCIChangeRecord);	
	virtual bool Connect();

	CCIInfoUpdateController *m_pCIInfoUpdateController;
	CCIInfoChangeETLController *m_pCIInfoChangeETLController;
	CCIInfoChangeETLModel *m_pCIInfoChangeETLModel;	
};