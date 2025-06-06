#include <iostream>
using namespace std;

int main()
{
    char char1, char2, choice;
    do
    {
        system("cls"); // clear screen
        cout << "Please enter Two characters : ";
        cin >> char1 >> char2;

        if (char1 < char2)
        {
            for (char i = char1; i <= char2; i++) //print inordered list
            {
                cout << i << " ";
            }
        }
        else if (char1 > char2)
        {
            for (char i = char1; i >= char2; i--) //print reverse order list
            {
                cout << i << " ";
            }
        }
        else
        {
            cout << "Wrong Input! Please enter difference characters!" << endl;
        }
        
        cout << "\n Do you want to repeat (Y/N)? ";
        cin >> choice;
    } while (choice == 'Y' || choice == 'y'); //repeat while choice is yes
    
    cout << endl;
    system("PAUSE");
    return 0;
}
