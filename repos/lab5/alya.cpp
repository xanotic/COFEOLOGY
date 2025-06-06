#include <iostream>
using namespace std;

int main() {
    char inputChar;
    cout << "Enter a character: ";
    cin >> inputChar;

    switch (inputChar) {
        case 'A':
        case 'a':
            cout << "vowel Apple" << endl;
            break;
        case 'E':
        case 'e':
            cout << "vowel Elephant" << endl;
            break;
        case 'I':
        case 'i':
            cout << "vowel Igloo" << endl;
            break;
        case 'O':
        case 'o':
            cout << "vowel Octopus" << endl;
            break;
        case 'U':
        case 'u':
            cout << "vowel Umbrella" << endl;
            break;
        default:
            cout << "consonant" << endl;
    }

    return 0;
}