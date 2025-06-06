#include<iostream>
using namespace std;

int main()
{
    int num1,num2;

    cout<<"Please enter 2 numbers : ";
    cin>>num1>>num2;
    if (num1 < num2)
    {
        cout<<endl<<" Sequential of numbers between "<<num1<<" and "<<num2<<" : "<<"- "<<endl<<endl;
        while (num1 <= num2)
        {
            cout<<num1<<" ";
            num1++;
        }
    }
    else
    {
        cout<<"sequential number in reverse order between" << num1 << " and " << num2 << " : "<<"- "<<endl;
        while (num1>=num2){
            cout<<num1<<" ";
            num1--;
        }
    }

    system("PAUSE");
    return 0;
}
