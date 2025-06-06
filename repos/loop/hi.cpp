#include <iostream>
using namespace std;
#include <iomanip>

int main()
{
    int a = 1;
    int b = 1;

    cout << "\t\tNumber\t\t" << "\t\tNumber Square" << endl;

    while (a <= 10 && b <= 10)
    {
        cout << setw(8) << "\t\t"<<a << "\t\t\t\t" << b * b << endl;
        a++;
        b++;
    }

    return 0;
}
