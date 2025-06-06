package q4;

public class HomeCare

{
    private String custName;
    private String phoneNo;
    private String location;
    private char serviceType;
    private int duration;
    Staff stf;

    public HomeCare(String custName, String phoneNo, String location, char serviceType, int duration, Staff stf)
    {
        this.custName = custName;
        this.phoneNo = phoneNo;
        this.location = location;
        this.serviceType = serviceType;
        this.duration = duration;
        this.stf = stf;
    }

    public void setHomeCare(String custName, String phoneNo, String location, char serviceType, int duration, Staff stf)
    {
        this.custName = custName;
        this.phoneNo = phoneNo;
        this.location = location;
        this.serviceType = serviceType;
        this.duration = duration;
        this.stf = stf;
    }

    public char getServiceType()
    {
        return serviceType;
    }

    public int getDuration()
    {
        return duration;
    }

    //answer b
    public double calcServiceCharge()
    {
        double charge = 0;

        if (getServiceType() == 'P')
        {
            charge = 60 * getDuration();
        }
        else if (getServiceType() == 'T')
        {
            charge = 120 * getDuration();
        }
        else if (getServiceType() == 'B')
        {
            charge = 80 * getDuration();
        }

        if (getDuration() > 5)
        {
            charge = charge * 0.95;
        }

        return charge;
    }

}
