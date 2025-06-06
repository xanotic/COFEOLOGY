#include <iostream>
#include <iomanip>
using namespace std;
#define UNDERLINE "\033[4m"
#define CLOSEUNDERLINE "\033[0m"

int main()
{

     string name, address, primarySchool, secondarySchool, highSchool, workingExp, workingExp2;
     int age;

     cout << "insert your full name : ";
     getline(cin, name);
     cout << "insert your age : ";
     cin >> age;
     cin.ignore();
     cout << "insert your full address : ";
     getline(cin, address);
     cout << "where did you study during your primary school : ";
     getline(cin, primarySchool);
     cout << "where did you study during your secondary school : ";
     getline(cin, secondarySchool);
     cout << "where did you study during your high school : ";
     getline(cin, highSchool);
     cout << "give me your first working experience  : ";
     getline(cin, workingExp);
     cout << "give me your second working experience  : ";
     getline(cin, workingExp2);

     cout << UNDERLINE << "MY RESUME" << CLOSEUNDERLINE << "\n\n";
     cout << UNDERLINE << "Personal Information" << CLOSEUNDERLINE << "\n\n";
     cout << left << setw(20) << "name"
          << ":" << name << "\n";
     cout << left << setw(20) << "Age"
          << ":" << age << "\n";
     cout << left << setw(20) << "Address"
          << ":" << address << "\n\n";
     cout << UNDERLINE << "Education Background" << CLOSEUNDERLINE << "\n\n";
     cout << left << setw(20) << "Primary School"
          << ":" << primarySchool << "\n";
     cout << left << setw(20) << "Secondary School"
          << ":" << secondarySchool << "\n";
     cout << left << setw(20) << "High School"
          << ":" << highSchool << "\n\n";
     cout << UNDERLINE << "Working Experience" << CLOSEUNDERLINE << "\n\n";
     cout << "   1.  " << workingExp << "\n";
     cout << "   2.  " << workingExp2 << "\n";

     return 0;
}