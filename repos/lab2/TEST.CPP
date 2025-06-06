#include <iostream>
#include <string>
#include <iomanip>
#define underline "\033[4m"
#define closeunderline "\033[0m"
using namespace std;

int main()
{
	
	string name,address,primarySchool,secondarySchool,higherSchool,workingExperience;
	int age;
	
	cout<<underline<< "MY RESUME"<< closeunderline<<"\n"<<endl;
	
	cout<<underline<<"Personal Information"<<closeunderline<<"\n\n"<<endl;
	
	cout<<endl<<"name:";
	cin>>name;
	
	cout<<"Age:";
	cin>>age;
	
	cout<<"Address:" ;
	cin>>address;
	cout<<"\n"<<endl;
	
	cout<<underline<<"Educational Background"<<closeunderline<<"\n\n"<<endl;
	
	cout<<"Primary School"<<setw(7)<<":";
	cin>>primarySchool;
	
	cout<<"Secondary School"<<setw(9)<<":";
	cin>>secondarySchool;
	
	cout<<"Higher School"<<setw(6)<<":";
	cin>>higherSchool;
	cout<<"\n"<<endl;
	
	cout<<underline<<"Working Experience"<<closeunderline<<"\n\n"<<endl;
	
	cout<<"insert your working experience:";
	cin>>workingExperience;
	
	return 0;
	
}