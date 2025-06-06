#include <iostream>
using namespace std;

int main()
{
    cout << "List of Odd numbers : ";

    for (int count = 1; count <=10; count++)
    {
        if (count%2 == 1)
        cout << count << " ";
    }
    cout << endl;

    cout << "List of Even numbers : ";
    
    for (int count = 1; count <=10; count++)
    {
        if (count%2 == 0)
        cout << count << " ";
    }

    cout << endl << endl;
    system("PAUSE");
    return 0;
}