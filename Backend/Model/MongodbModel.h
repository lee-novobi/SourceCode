#pragma once
#include "../Common/Common.h"
#include "mongo/client/dbclient.h"
#include "mongo/bson/bsonobjbuilder.h"
using namespace mongo;

typedef vector<BSONObj> BSONObjArray;
class CMongodbModel
{
public:
	CMongodbModel(void);
	CMongodbModel(const BSONObj& objBSON);
	~CMongodbModel(void);

	operator BSONObj() { return m_objBSON; }
	operator BSONObj*() { return &m_objBSON; }

	inline CMongodbModel operator=(const BSONObj& objBSON) { m_objBSON = objBSON; return *this;}
	inline void UpdateBSONObj(const BSONObj& objBSON) { m_objBSON = objBSON; }
	inline BSONElement GetOID() { return m_objBSON["_id"]; }

	static BSONObj RemoveFields(BSONObj *pRecord, StringArray &arrFieldName);
	static BSONObj MergeBSONObj(BSONObj *pOldRecord, BSONObj *pNewRecord);
	static BSONObj MergeBSONObj(BSONObj *pOldRecord, BSONObj *pNewRecord, BSONObj &pboChangedFields);

protected:
	BSONObjBuilder *m_pBSONBuilder;
	BSONObj m_objBSON;
};
