#pragma once
#include "MongodbModel.h"

class CCIInfoChangeModel:
	public CMongodbModel
{
public:
	CCIInfoChangeModel(void);
	~CCIInfoChangeModel(void);
	virtual vector<BSONObj> GetMiningCursor(BSONObj boCIInfoRecord);
	virtual BSONObjArray GetCIInfoChangeCursor(BSONElement beObjID, BSONObj boOldCIInfoRecord, BSONObj boNewCIInfoRecord);
	virtual void PushValueToCursor(vector<BSONObj> &vInfoCursor, string strOldVal, string strNewVal, string strFieldName, BSONElement beObjID);
	virtual void PushArrayValueToCursor(vector<BSONObj> &vInfoCursor, BSONElement beArrayInfo, string strFieldArrayName, BSONElement beObjID);
	virtual void PushArrayValueToCursor(vector<BSONObj> &vInfoCursor, BSONElement beArrayOldInfo, BSONElement beArrayNewInfo, string strFieldArrayName, BSONElement beObjID);
protected:
	virtual void InitLookUpFieldValue(){};
	string MapLookUpField(string strFieldName, string strVal);
	map< string, map<string, string> > m_mapLookUpFieldValue;
	bool m_bIsValueMapping;
};