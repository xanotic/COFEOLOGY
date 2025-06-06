#include <iostream>
using namespace std;

int main (){
    for (int i = 1; i <= 7; i++)
    {
        if (i == 1 || i == 7){
            for (int k = 1; k <= 6; k++)
            {
                cout << "$";
            }
            cout << endl;
        }
        else 
        {
            cout << "$";
            for (int k = 1; k <= 4; k++)
            {
                cout << " ";
            }
            cout << "$";
            cout << endl;
        }
    }
    
}