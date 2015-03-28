#include "MongodbModel.h"
#include "../Common/DBCommon.h"
CMongodbModel::CMongodbModel(void)
{	
}

CMongodbModel::CMongodbModel(const BSONObj& objBSON)
{
	m_objBSON = objBSON;
}

CMongodbModel::~CMongodbModel(void)
{
}

BSONObj CMongodbModel::RemoveFields(BSONObj *pRecord, StringArray &arrFieldName) {
	BSONObjBuilder b;
	BSONObjIterator i(*pRecord);
	while ( i.more() ) {
		BSONElement e = i.next();
		const char *fname = e.fieldName();
		StringArray::iterator it = find(arrFieldName.begin(), arrFieldName.end(), fname);
		if (it == arrFieldName.end())
			b.append(e);
	}
	return b.obj();
}

BSONObj CMongodbModel::MergeBSONObj(BSONObj *pOldRecord, BSONObj *pNewRecord)
{
	BSONObjBuilder bobMergedRecord;
	StringSet setFieldNames;
	StringSet::iterator it;
	string strFieldName;
	pOldRecord->getFieldNames(setFieldNames);
	bobMergedRecord.appendElements(*pNewRecord);
	for(it=setFieldNames.begin(); it!=setFieldNames.end(); it++)
	{
		strFieldName = *it;
		if(strFieldName.compare("_id") == 0)
		{
			continue;
		}
		if(!pNewRecord->hasField(strFieldName))
		{
			bobMergedRecord.append(pOldRecord->getField(strFieldName));
		}
	}
	return bobMergedRecord.obj();
}

BSONObj CMongodbModel::MergeBSONObj(BSONObj *pOldRecord, BSONObj *pNewRecord, BSONObj &pboChangedFields)
{
	BSONObjBuilder bobMergedRecord;
	BSONObjBuilder bobChangedFields;
	StringSet setFieldNames;
	StringSet::iterator it;
	string strFieldName;
	bobMergedRecord.appendElements(*pNewRecord);
	if(pOldRecord->isEmpty()){
		pNewRecord->getFieldNames(setFieldNames);
	}
	else{
		pOldRecord->getFieldNames(setFieldNames);
	}
	for(it=setFieldNames.begin(); it!=setFieldNames.end(); it++)
	{
		strFieldName = *it;
		if(strFieldName.compare("_id") == 0)
		{
			continue;
		}
		if(!pNewRecord->hasField(strFieldName))
		{
			bobMergedRecord.append(pOldRecord->getField(strFieldName));
		}
		else if(!pOldRecord->hasField(strFieldName))
		{
			bobChangedFields.append(strFieldName, 1);
		}
		else if((*pNewRecord)[strFieldName] != (*pOldRecord)[strFieldName])
		{
			bobChangedFields.append(strFieldName, 1);
		}
	}
	pboChangedFields = bobChangedFields.obj();
	return bobMergedRecord.obj();
}