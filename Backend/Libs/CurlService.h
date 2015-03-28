#include <curl/curl.h>
#include <curl/easy.h>
#include <string.h>
#include <iostream>

using namespace std;

//#define HEADER "Content-Type: application/json"
#define HEADER "Content-Type: text/xml"
#define COOKIE_PATH "cookielol.txt"
#define USER_AGENT "Mozilla/5.0 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35"

struct MemoryStruct {
  char *memory;
  size_t size;
};

class CCurlService
{
public:
	CCurlService(void);
	~CCurlService(void);
	
	bool CallLink(char* pUrl, char* pUsername, char* pPassword, char* pField, char** _pReturn);
	static size_t WriteCallback(void *contents, size_t size, size_t nmemb, void *userp){
		((string*)userp)->append((char*)contents, size * nmemb);
		return size * nmemb;
	}
};

