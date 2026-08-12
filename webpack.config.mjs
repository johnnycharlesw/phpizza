import path from 'node:path';
import url from 'node:url';

const __dirname = path.dirname(url.fileURLToPath(import.meta.url));

const mode = process.env.NODE_ENV === 'production' ? 'production' : 'development';

/** @type {import('webpack').Configuration} */
export default {
  mode,
  target: 'web',
  devtool: mode === 'production' ? 'source-map' : 'eval-source-map',
  entry: {
    videoplayer: './client_src/videoplayer.ts',
    console_message: './client_src/console_message.ts',
    mistral_backed_agent_ui: './client_src/mistral-backed-agent-ui.ts',
    phpizza_desktop: './client_src/phpizza-desktop.ts'
  },
  output: {
    path: path.resolve(__dirname, 'assets/phpizza-client-scripts'),
    filename: '[name].js',
    clean: false,
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js'],
    extensionAlias: {
      '.js': ['.ts', '.tsx', '.js'],
    },
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        loader: 'ts-loader',
        exclude: /node_modules/,
        options: {
          configFile: 'tsconfig.json',
          transpileOnly: true,
        },
      },
    ],
  },
};
