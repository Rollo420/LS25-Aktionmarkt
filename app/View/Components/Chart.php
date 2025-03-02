namespace App\View\Components;

use Illuminate\View\Component;

class Chart extends Component
{
    public $type;
    public $data;
    public $options;

    public function __construct($type, $data, $options)
    {
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.chart');
    }
}
