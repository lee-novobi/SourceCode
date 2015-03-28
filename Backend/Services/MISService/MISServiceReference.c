#include "stdio.h"
#include <iostream>
#include <string>
#include <unistd.h>		/* defines _POSIX_THREADS if pthreads are available */
#if defined(_POSIX_THREADS) || defined(_SC_THREADS)
#include <pthread.h>
#endif
#include <signal.h>		/* defines SIGPIPE */
#include "../Common/ExternalCommon.h"
#include "../Libs/CurlService.h"
using namespace std;

extern "C"  char* GetMISDataInfo(char* pField)
{	
	CCurlService *pCurl = new CCurlService();
	char *pResponse = NULL;
	string strUserName = "sdk_intf";
	string strPasswd = "Vng@SDK#intf!2438035";
	
	pCurl->CallLink((char*)MIS_ORGCHART_URL, (char*)strUserName.c_str(), (char*)strPasswd.c_str(), pField, &pResponse);

	if (NULL != pCurl)
		delete pCurl;

	return pResponse;
}

extern "C"  char* GetMISUserInfo(char* pField)
{	
	CCurlService *pCurl = new CCurlService();
	char *pResponse = NULL;
	string strUserName = "misesb";
	string strPasswd = "Vng@123";

	pCurl->CallLink((char*)MIS_ESB_ORGCHART_URL, (char*)strUserName.c_str(), (char*)strPasswd.c_str(), pField, &pResponse);
	
	if (NULL != pCurl)
		delete pCurl;

	return pResponse;
}
