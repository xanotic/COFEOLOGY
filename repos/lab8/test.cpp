#include <iostream>
using namespace std;

int main(){
int number = 0, counter = 0, value1 = 100, value2 = 0;
while (counter < 5)
{
cout << "\n Enter any number ";
cin >> number;
if (number < value1)
value1 = number;
if (number > value2)
value2 = number;
counter++;
}
cout << "\n The ******** number is " << value2;
cout << "\n The ######## number is " << value1;

return 0;
}