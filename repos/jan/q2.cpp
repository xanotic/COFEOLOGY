#include <iostream>
#include <string>
using namespace std;

int main(){

    double weight, charges,charges2, input, totalCharge;
    char deco;
    cout <<"insert weight of cake : ";
    cin >> input;

    if (input >= 0 && input < 400)
        charges = 0.05*input;
    else if (input >= 400 && input <800)
        charges = (400*0.05) + (input*0.08);
    else if (input >=800)
        charges = (400*0.05) + (400*0.08) + (input*0.10);
    
    cout << "do you want extra deco? (Y/N) : ";
    cin >> deco;

    if (deco == 'Y')
    {
        charges2 = 10;
    }
    
    totalCharge = charges+charges2;

    cout << "total charge is : " << totalCharge;

    return 0;

}