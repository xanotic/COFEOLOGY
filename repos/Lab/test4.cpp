#include <iostream>
using namespace std;

int main() {

int quantity;
string alert;

cin >> quantity;

if (quantity < 10)
{
 cout << "high";
}
else if (quantity < 25)
{
cout << "Medium";
}
else if (quantity < 50)
{
cout << "Low";
}



    return 0;
}