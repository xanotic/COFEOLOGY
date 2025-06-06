#include <iostream>

using namespace std;

int main()
{
    // a [rphram to know whether the number is odd or even]
    int x;

    cout << "insert a number : ";
    cin >> x;

    if (x % 2 == 0)
    {
        cout << x << " is an even number";
    }
    else
    {
        cout << x << " is an odd number";
    }

    return 0;
}