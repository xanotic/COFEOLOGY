#include <iostream>
#include <string>
#include <vector>

using namespace std;
//To insert temperature for 3 days using array and display the smallest temperature.
int main(){

double temperature[3], smallest, lowest = 40;

for (int i = 0; i < sizeof(temperature)/ sizeof(temperature[0]); i++)
{
    cout << "insert your temperature : " << endl;
    cin >> temperature[i];
    if(temperature[i] < lowest)
    lowest = temperature[i];
    
}
    cout << "lowest temperature is " << lowest << endl;

    
}