#include <iostream>
using namespace std;

void alphabet(char x)
{
    switch(x)
    {
    case 'A':
    case 'a':
    cout << "vowel apple";
    break;
    case 'E':
    case 'e':
    cout << "vowel elephant";
    break;
    case 'I':
    case 'i':
    cout << "vowel igloo";
    break;
    case 'O':
    case 'o':
    cout << "vowel octopus";
    break;
    case 'U':
    case 'u':
    cout << "vowel umbrella";
    break;
    default:
    cout << "consonant";
    break;
    }
}
    int main ()
    {
        char x;
        cout << "Enter an alphabet: ";
        cin >> x;
        alphabet(x);
        return 0;
    }