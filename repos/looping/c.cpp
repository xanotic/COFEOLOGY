#include <iostream>
using namespace std;

int main()
{
    char ans;
    bool exitLoop = false;

    while (!exitLoop)
    {
        cout << "y/n: ";
        cin >> ans;

        if (ans == 'y' || ans == 'Y')
        {
            exitLoop = true;
        }
        else if (ans == 'n' || ans == 'N')
        {
            // Handle 'n' input if needed
        }
        else
        {
            cout << "Invalid input. Please enter 'y' or 'n'." << endl;
        }
    }

    cout << "Exiting the loop." << endl;
    return 0;
}
