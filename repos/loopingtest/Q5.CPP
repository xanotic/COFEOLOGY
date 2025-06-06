#include <iostream>
using namespace std;

int main (){
    for (int i = 7; i >= 1; i--)
    {
        for (int j = 7-i ; j >= 1; j--)
        {
            cout << " "; 
        }
        
        for (int k = i; k >= 1; k--)
        {
            cout << "*";
        }
        cout << endl;
        
    }
    
}