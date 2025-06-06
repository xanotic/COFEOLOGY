#include <iostream>
using namespace std;

int main (){
    for (int i = 1; i <= 5; i++)
    {
        if (i == 1 || i == 2 || i == 3)
        {
            for (int k = 1; k <= 5; k++)
            {
                cout << "$";
            }
            cout << endl;
        }
        else
        {
            for (int k = 1; k <= 5; k++)
            {
                cout << "#";
            }
            cout << endl;
        }
    }
}
