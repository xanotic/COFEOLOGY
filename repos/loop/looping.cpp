#include <iostream>
using namespace std;

int main()
{
    int a = 1;
    int b = 1;

    cout << "number" << endl;

    while (a <= 10)
    {
        cout << a << endl;
        a++;
    }

    cout << endl << "number square"<< endl;
    while (b <= 10)
    {
        cout << b*b << endl;
        b++;
    }
    return 0;
}