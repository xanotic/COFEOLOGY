#include <iostream>
#include <string>
using namespace std;

int main() 
{
    string state, meterType;
    double waterUse, charges;
    char domSupply;

    //input 
    cout << "PLEASE START YOUR ANSWER WITH CAPITAL LETTER :)" << endl;
    cout << "Enter your amount of water usage: ";
    cin >> waterUse;
    cin.ignore();
    cout << "What state do you live? (Negeri Sembilan / Melaka / Terengganu): " << endl;
    getline (cin, state);
    cout << "Do you have a domestic supplies? (Y/N): ";
    cin >> domSupply;
    if (domSupply == 'Y')
    {
        cout << "Individual or Bulk?: ";
        cin >> meterType;
    }

    // charges calc Negeri Sembilan
    if (state == "Negeri Sembilan" &&  domSupply ==  'Y' && meterType == "Individual" )
    {
        if (waterUse >= 0 && waterUse <= 20)
        {
            charges = waterUse * 0.55;
            if (charges <= 5)
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 20 && waterUse <= 35)
        {
            charges = (waterUse - 20) * 0.85 + 11;
        }
        else if (waterUse >35)
        {
            charges = (waterUse - 35) * 1.40 + 23.75;
        }      
    }
    else if (state == "Negeri Sembilan" && domSupply == 'Y' && meterType == "Bulk")
    {
        charges = waterUse * 1.40;
        if (charges <= 30)
        {
            charges = 0;
            cout << "Your charges have been totally subsided by the government "<< endl;
        }
    }
    else if (state == "Negeri Sembilan" && domSupply == 'N')
    {
        if (waterUse >= 0 && waterUse <= 35)
        {
            charges = waterUse * 1.85;
            if (charges <= 18.5) 
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 35)
        {
            charges = (waterUse - 35) * 2.7 + 64.75;
        }
    }

    // charges calc Melaka
    else if (state == "Melaka" &&  domSupply ==  'Y' && meterType == "Individual" )
    {
        if (waterUse >= 0 && waterUse <= 20)
        {
            charges = waterUse * 0.7;
            if (charges <= 7)
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 20 && waterUse <= 35)
        {
            charges = (waterUse - 20) * 1.15 + 14;
        }
        else if (waterUse >35)
        {
            charges = (waterUse - 35) * 1.75 + 31.25;
        }      
    }
    else if (state == "Melaka" && domSupply == 'Y' && meterType == "Bulk")
    {
        charges = waterUse * 1.80;
        if (charges <= 25)
        {
            charges = 0;
            cout << "Your charges have been totally subsided by the government "<< endl;
        }
    }
    else if (state == "Melaka" && domSupply == 'N')
    {
        if (waterUse >= 0 && waterUse <= 35)
        {
            charges = waterUse * 2.4;
            if (charges <= 25) 
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 35)
        {
            charges = (waterUse - 35) * 2.45 + 84;
        }
    }

    // charges calc Terengganu
    else if (state == "Terengganu" &&  domSupply ==  'Y' && meterType == "Individual" )
    {
        if (waterUse >= 0 && waterUse <= 20)
        {
            charges = waterUse * 0.42;
            if (charges <= 7)
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 20 && waterUse <= 35)
        {
            charges = (waterUse - 20) * 0.65 + 8.4;
        }
        else if (waterUse >35)
        {
            charges = (waterUse - 35) * 0.9 + 18.15;
        }      
    }
    else if (state == "Terengganu" && domSupply == 'Y' && meterType == "Bulk")
    {
        charges = waterUse * 0.62;
        if (charges <= 6.2)
        {
            charges = 0;
            cout << "Your charges have been totally subsided by the government "<< endl;
        }
    }
    else if (state == "Terengganu" && domSupply == 'N')
    {
        if (waterUse >= 0 && waterUse <= 35)
        {
            charges = waterUse * 1;
            if (charges <= 15) 
            {
                charges = 0;
                cout << "Your charges have been totally subsided by the government "<< endl;
            }
        }
        else if (waterUse > 35)
        {
            charges = (waterUse - 35) * 1.4 + 35;
        }
    }

    //invalid data
    else
    {
        cout << "Invalid data, Please enter the correct information.";
        return 1;
    }

    //output
    cout << "Your total charges is : RM " << charges;

    return 0;
}