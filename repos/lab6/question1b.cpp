#include <iostream>
using namespace std;

int main()
{
    int counter=10;

    cout<<"\n\n\t START... Press <Enter>";

    cin.get(); // press enter
    while(counter >= 1)
    {
        system("cls"); // clear screen
        cout<<"\n\n\n\n\n\n\t\t\t"<<counter; // newline
        // print value of counter
        cin.get(); // input character or press enter

        counter--;
    }

    system("cls");
    // clear screen
    cout<<"\n\n\n\n\n\n\t\t\t KABOOOOMMMM!!! "<<endl<<endl;

    cin.get(); //Press Enter to exit
    return 0;
}
