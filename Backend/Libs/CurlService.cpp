#include "CurlService.h"

CCurlService::CCurlService(void)
{
}

CCurlService::~CCurlService(void)
{
}

bool CCurlService::CallLink(char* pUrl, char* pUsername, char* pPassword, char* pField, char** _pReturn)
{
	bool bResult = true;
	char *pDataInfo = NULL;

	CURL *curl;
	//curl_global_init(CURL_GLOBAL_ALL);

	CURLcode res;
	string readBuffer;

	string strUrl;
	string strParameter;

	strUrl = pUrl;
	
	if (NULL != pField)
	{
		strParameter = pField;
		strUrl += strParameter;
	}
	
	cout << "URL:" << strUrl << endl;
	struct curl_slist *headers = NULL;
	headers = curl_slist_append(headers, HEADER);
	curl = curl_easy_init();
	curl_easy_setopt(curl, CURLOPT_URL, (char*)strUrl.c_str());

	if (NULL != pUsername)
		curl_easy_setopt(curl, CURLOPT_USERNAME, pUsername); 

	if (NULL != pPassword)
		curl_easy_setopt(curl, CURLOPT_PASSWORD, pPassword); 
	
    curl_easy_setopt(curl, CURLOPT_TIMEOUT, 60);
    curl_easy_setopt(curl, CURLOPT_USERAGENT, USER_AGENT);
    curl_easy_setopt(curl, CURLOPT_HEADER, 0);
	//curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, "PUT");
	 
	/*if(NULL != pField) {
		curl_easy_setopt(curl, CURLOPT_POST, 1);
		curl_easy_setopt(curl, CURLOPT_POSTFIELDS, pField);
	}*/
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &readBuffer);
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER,headers);
	
    res = curl_easy_perform(curl);
	if(res != CURLE_OK) {
		bResult = false;
	}
	else
	{
		int nDataLength = 0;
		nDataLength = readBuffer.size();
		pDataInfo = new char[nDataLength + 1];
		pDataInfo[nDataLength] = '\0';

		//// Copy result to memory
		strcpy(pDataInfo, readBuffer.c_str());
		*_pReturn = pDataInfo;
	}
    curl_easy_cleanup(curl);
	//curl_global_cleanup();

	return bResult;
}

